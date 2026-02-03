<template>
    <div class="help-chatbot-container" ref="container">
        <button 
            class="help-chatbot-button" 
            @click="toggleDropdown"
            :class="{ active: showDropdown }"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
        </button>

        <!-- Dropdown -->
        <Teleport to="body">
            <div 
                v-if="showDropdown" 
                class="help-chatbot-dropdown" 
                :style="dropdownStyle"
                @click.stop
            >
                <div class="help-chatbot-header">
                    <h3>Need Help?</h3>
                    <button @click="closeDropdown" class="close-dropdown-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>

                <div class="help-chatbot-body">
                    <div v-if="selectedQuestion === null" class="help-chatbot-questions">
                        <div 
                            v-for="(question, index) in questions" 
                            :key="index"
                            class="help-chatbot-question-btn"
                            @click.stop="selectQuestion(index)"
                        >
                            <span>{{ question.question }}</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 18l6-6-6-6"/>
                            </svg>
                        </div>
                    </div>
                    <div v-else class="help-chatbot-answer">
                        <div class="help-chatbot-answer-header">
                            <button class="help-chatbot-back-btn" @click="selectedQuestion = null">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                                </svg>
                                <span>Back</span>
                            </button>
                        </div>
                        <div class="help-chatbot-answer-content">
                            <div class="help-chatbot-answer-question">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/>
                                </svg>
                                <p>{{ questions[selectedQuestion].question }}</p>
                            </div>
                            <div class="help-chatbot-answer-text">
                                <p>{{ questions[selectedQuestion].answer }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script>
export default {
    name: 'HelpChatbot',
    props: {
        userRole: {
            type: String,
            default: 'student'
        }
    },
    data() {
        return {
            showDropdown: false,
            selectedQuestion: null,
            dropdownStyle: {},
        };
    },
    computed: {
        questions() {
            if (this.userRole === 'client') {
                return [
                    {
                        question: "How do I post a new job?",
                        answer: "Go to 'Post Job' in the navigation menu, fill out the job details including title, description, requirements, budget, and location. Once submitted, your job will be visible to students looking for opportunities."
                    },
                    {
                        question: "How do I manage my posted jobs?",
                        answer: "View all your posted jobs from your dashboard. You can edit job details, view applications, and manage the status of each job posting."
                    },
                    {
                        question: "How do I review applications?",
                        answer: "Click on any job posting from your dashboard to see all applications. You can review student profiles, view their resumes, and accept or reject applications directly from the job details page."
                    },
                    {
                        question: "Can I edit or delete a job posting?",
                        answer: "Yes! You can edit your job postings at any time by clicking on the job and selecting 'Edit'. You can also close or delete job postings when they're no longer needed."
                    },
                    {
                        question: "How do I contact applicants?",
                        answer: "Use the messaging feature to communicate with applicants. Click on the chat icon in the header to send messages and coordinate with potential candidates."
                    },
                    {
                        question: "How do I set the budget for a job?",
                        answer: "When creating or editing a job, you'll see a budget field. Enter the total amount you're willing to pay for the completed work. This helps attract students whose expectations align with your budget."
                    },
                    {
                        question: "What happens after I accept an application?",
                        answer: "Once you accept an application, the student will be notified. You can then use the messaging feature to coordinate details, discuss project requirements, and set up the working arrangement."
                    },
                    {
                        question: "Can I repost a job?",
                        answer: "Yes! You can repost closed jobs by editing them and changing their status to active. This is useful if you need to fill the same position again."
                    }
                ];
            } else {
                // Student questions
                return [
                    {
                        question: "How do I apply for a job?",
                        answer: "Browse available jobs from the 'Browse Jobs' section, click on a job that interests you, review the details, and click 'Apply Now'. Make sure your profile is complete before applying!"
                    },
                    {
                        question: "How do I track my applications?",
                        answer: "All your applications are displayed on your dashboard under 'My Applications'. You can see the status of each application (Pending, Accepted, or Rejected) and view details by clicking on them."
                    },
                    {
                        question: "What happens after I apply?",
                        answer: "Your application will be reviewed by the employer. You'll see the status update on your dashboard. If accepted, the employer will contact you with next steps. Check your dashboard regularly for updates!"
                    },
                    {
                        question: "Can I edit my profile?",
                        answer: "Yes! Go to your profile page from the navigation menu. You can update your information, add skills, upload a profile picture, and modify your resume at any time."
                    },
                    {
                        question: "How do I find jobs that match my skills?",
                        answer: "Use the 'Browse Jobs' page to filter jobs by category, location, budget, and other criteria. You can also use the search feature to find specific job titles or keywords."
                    },
                    {
                        question: "What should I include in my application?",
                        answer: "Make sure your profile is complete with your skills, experience, and a professional profile picture. When applying, you can include a cover letter explaining why you're a good fit for the position."
                    },
                    {
                        question: "How do I know if a job is still available?",
                        answer: "Jobs that are no longer accepting applications will be marked accordingly. Always check the job posting date and status before applying to ensure it's still open."
                    },
                    {
                        question: "Can I withdraw an application?",
                        answer: "Yes, you can withdraw your application from the application details page. Go to 'My Applications', select the application, and use the withdraw option if available."
                    }
                ];
            }
        }
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        toggleDropdown() {
            this.showDropdown = !this.showDropdown;
            if (this.showDropdown) {
                this.updateDropdownPosition();
            }
        },
        closeDropdown() {
            this.showDropdown = false;
            this.selectedQuestion = null;
        },
        selectQuestion(index) {
            if (index !== null && index !== undefined && index >= 0 && index < this.questions.length) {
            this.selectedQuestion = index;
            }
        },
        updateDropdownPosition() {
            this.$nextTick(() => {
                const button = this.$refs.container?.querySelector('.help-chatbot-button');
                if (button) {
                    const rect = button.getBoundingClientRect();
                    const windowWidth = window.innerWidth;
                    const windowHeight = window.innerHeight;
                    const dropdownWidth = 420;
                    const dropdownHeight = 600;
                    const spaceOnRight = windowWidth - rect.right;
                    const spaceBelow = windowHeight - rect.bottom;
                    
                    let top, right, left;
                    
                    if (spaceBelow < dropdownHeight && rect.top > dropdownHeight) {
                        top = `${rect.top - dropdownHeight - 10}px`;
                    } else {
                        top = `${rect.bottom + 10}px`;
                    }
                    
                    if (spaceOnRight >= dropdownWidth - 50) {
                        right = `${windowWidth - rect.right - 10}px`;
                        left = 'auto';
                    } else {
                        right = '1rem';
                        left = 'auto';
                    }
                    
                    this.dropdownStyle = {
                        top,
                        right,
                        left,
                        animation: 'slideDown 0.2s ease',
                    };
                }
            });
        },
        handleClickOutside(event) {
            if (this.showDropdown) {
                const container = this.$refs.container;
                const dropdown = document.querySelector('.help-chatbot-dropdown');
                if (container && !container.contains(event.target) && 
                    dropdown && !dropdown.contains(event.target)) {
                    this.closeDropdown();
                }
            }
        },
    },
};
</script>

<style scoped>
.help-chatbot-container {
    position: relative;
}

.help-chatbot-button {
    position: relative;
    width: 36px;
    height: 36px;
    border: none;
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    transition: all 0.2s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.help-chatbot-button:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #FFFFFF;
    border-color: rgba(255, 255, 255, 0.2);
}

.help-chatbot-button.active {
    background: rgba(31, 106, 225, 0.2);
    color: #FFFFFF;
    border-color: rgba(31, 106, 225, 0.3);
}

.help-chatbot-button svg {
    width: 18px;
    height: 18px;
}

/* Dropdown */
.help-chatbot-dropdown {
    position: fixed;
    width: 420px;
    max-width: calc(100vw - 2rem);
    max-height: 600px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    z-index: 10001;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.help-chatbot-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
}

.help-chatbot-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
    color: #111827;
}

.close-dropdown-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: #64748b;
    cursor: pointer;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.close-dropdown-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

.close-dropdown-btn svg {
    width: 18px;
    height: 18px;
}

.help-chatbot-body {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.help-chatbot-questions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.help-chatbot-question-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem 1rem;
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
    color: #111827;
    font-weight: 500;
}

.help-chatbot-question-btn:hover {
    background: #f9fafb;
    border-color: #6366f1;
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
}

.help-chatbot-question-btn svg {
    width: 16px;
    height: 16px;
    color: #6366f1;
    flex-shrink: 0;
    margin-left: 0.5rem;
    transition: transform 0.2s ease;
}

.help-chatbot-question-btn:hover svg {
    transform: translateX(2px);
}

.help-chatbot-answer {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.help-chatbot-answer-header {
    margin-bottom: 1rem;
}

.help-chatbot-back-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgba(99, 102, 241, 0.1);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: 8px;
    color: #6366f1;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.help-chatbot-back-btn:hover {
    background: rgba(99, 102, 241, 0.15);
    border-color: rgba(99, 102, 241, 0.3);
    transform: translateX(-2px);
}

.help-chatbot-back-btn svg {
    width: 16px;
    height: 16px;
}

.help-chatbot-answer-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.help-chatbot-answer-question {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
    border-radius: 12px;
    border: 1px solid rgba(99, 102, 241, 0.2);
}

.help-chatbot-answer-question svg {
    width: 20px;
    height: 20px;
    color: #6366f1;
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.help-chatbot-answer-question p {
    margin: 0;
    font-size: 0.9375rem;
    font-weight: 600;
    color: #111827;
    line-height: 1.5;
}

.help-chatbot-answer-text {
    padding: 1rem;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.help-chatbot-answer-text p {
    margin: 0;
    font-size: 0.875rem;
    color: #4b5563;
    line-height: 1.6;
}

/* Scrollbar styling */
.help-chatbot-body::-webkit-scrollbar {
    width: 8px;
}

.help-chatbot-body::-webkit-scrollbar-track {
    background: transparent;
}

.help-chatbot-body::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
}

.help-chatbot-body::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}

@media (max-width: 768px) {
    .help-chatbot-dropdown {
        width: calc(100vw - 2rem);
        max-height: 80vh;
        left: 1rem !important;
        right: 1rem !important;
        transform: none !important;
    }
}
</style>

